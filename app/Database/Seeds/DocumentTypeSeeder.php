<?php namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;

class DocumentTypeSeeder extends Seeder
{
    public function run()
    {
        $documentTypes = [
            [
                'name' => 'Research Papers',
                'slug' => 'research-papers',
                'color' => '#3b82f6' // blue
            ],
            [
                'id' => '616ff604-9e56-4890-af22-3c0413960484',
                'name' => 'Case Studies',
                'slug' => 'case-studies',
                'color' => '#10b981' // emerald
            ],
            [
                'id' => '14a33fea-3fce-45e1-87ab-13edb6329f4e',
                'name' => 'Policy Briefs',
                'slug' => 'policy-briefs',
                'color' => '#f59e0b' // amber
            ],
            [
                'id' => '3f72f1c6-5cc5-47b7-8d4a-6cbe3099c84b',
                'name' => 'Training Materials',
                'slug' => 'training-materials',
                'color' => '#ef4444' // red
            ],
            [
                'id' => '0328bbdb-cbfb-404f-bc53-8f8bf8ffd287',
                'name' => 'Technical Reports',
                'slug' => 'technical-reports',
                'color' => '#8b5cf6' // violet
            ],
            [
                'id' => '6823afbf-4af7-4de7-9034-42655c3ef022',
                'name' => 'Guidelines',
                'slug' => 'guidelines',
                'color' => '#ec4899' // pink
            ],
            [
                'id' => '6f96fea6-6b4f-44c2-be19-623779f0b0a8',
                'name' => 'Manuals',
                'slug' => 'manuals',
                'color' => '#14b8a6' // teal
            ],
            [
                'id' => '2a558268-3758-4f8a-a67f-15368316b050',
                'name' => 'Presentations',
                'slug' => 'presentations',
                'color' => '#f97316' // orange
            ]
        ];

        foreach ($documentTypes as $type) {
            $this->createDocumentType($type);
        }
    }

    protected function createDocumentType(array $data)
    {
        $db = \Config\Database::connect();
        
        // Check if document type already exists
        $exists = $db->table('document_types')
                   ->where('slug', $data['slug'])
                   ->countAllResults();
        
        if (!$exists) {
            $typeData = [
                'id' => Uuid::uuid4()->toString(),
                'name' => $data['name'],
                'slug' => $data['slug'],
                'color' => $data['color'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $db->table('document_types')->insert($typeData);
        }
    }
}