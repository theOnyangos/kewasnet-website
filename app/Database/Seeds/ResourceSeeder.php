<?php namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;

class ResourceSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Helper functions to get IDs
        $getPillarId = function($slug) use ($db) {
            return $db->table('pillars')
                    ->select('id')
                    ->where('slug', $slug)
                    ->get()
                    ->getRow()->id ?? null;
        };
        
        $getCategoryId = function($slug, $pillarId) use ($db) {
            return $db->table('resource_categories')
                    ->select('id')
                    ->where('slug', $slug)
                    ->where('pillar_id', $pillarId)
                    ->get()
                    ->getRow()->id ?? null;
        };
        
        $getDocumentTypeId = function($slug) use ($db) {
            return $db->table('document_types')
                    ->select('id')
                    ->where('slug', $slug)
                    ->get()
                    ->getRow()->id ?? null;
        };
        
        // Get required IDs
        $washPillarId = $getPillarId('wash');
        $policyCategoryId = $getCategoryId('policy-advocacy', $washPillarId);
        $researchCategoryId = $getCategoryId('research-innovation', $washPillarId);
        $communityCategoryId = $getCategoryId('community-programs', $washPillarId);
        
        $researchPaperTypeId = $getDocumentTypeId('research-papers');
        $caseStudyTypeId = $getDocumentTypeId('case-studies');
        $policyBriefTypeId = $getDocumentTypeId('policy-briefs');
        $trainingTypeId = $getDocumentTypeId('training-materials');
        
        // Sample resources data
        $resources = [
            [
                'title' => 'Impact of Community-Led Total Sanitation in Rural Kenya',
                'slug' => 'impact-clts-rural-kenya',
                'description' => 'A comprehensive study evaluating the effectiveness of CLTS approaches in improving sanitation outcomes across 50 rural communities.',
                'pillar_id' => $washPillarId,
                'category_id' => $communityCategoryId,
                'document_type_id' => $researchPaperTypeId,
                'publication_year' => '2023',
                'image_url' => 'https://images.unsplash.com/photo-1561484930-974554019ade',
                'file_url' => '/uploads/resources/clts-study-2023.pdf',
                'file_size' => '2.4MB',
                'file_type' => 'PDF',
                'is_featured' => 1,
                'download_count' => 124,
                'view_count' => 568
            ],
            [
                'id' => 'c9d6d12d-b693-477a-90ff-309984ef045d',
                'title' => 'Urban Water Access Solutions in Nairobi Informal Settlements',
                'slug' => 'urban-water-access-nairobi',
                'description' => 'Documenting successful models for improving water access in Nairobi\'s informal settlements through public-private partnerships.',
                'pillar_id' => $washPillarId,
                'category_id' => $communityCategoryId,
                'document_type_id' => $caseStudyTypeId,
                'publication_year' => '2022',
                'image_url' => 'https://images.unsplash.com/photo-1606761568499-6d2451b23c66',
                'file_url' => '/uploads/resources/urban-water-case-study.pdf',
                'file_size' => '1.8MB',
                'file_type' => 'PDF',
                'is_featured' => 1,
                'download_count' => 89,
                'view_count' => 432
            ],
            [
                'id' => 'ce71e9a6-cfed-4b31-8f94-04d702ac3ed3',
                'title' => 'National WASH Policy Implementation Progress Report',
                'slug' => 'national-wash-policy-report',
                'description' => 'An assessment of Kenya\'s progress in implementing the National WASH Policy 2021-2030 with recommendations for acceleration.',
                'pillar_id' => $washPillarId,
                'category_id' => $policyCategoryId,
                'document_type_id' => $policyBriefTypeId,
                'publication_year' => '2023',
                'image_url' => 'https://images.unsplash.com/photo-1551524559-8af4e6624178',
                'file_url' => '/uploads/resources/wash-policy-report.pdf',
                'file_size' => '3.1MB',
                'file_type' => 'PDF',
                'download_count' => 156,
                'view_count' => 721
            ],
            [
                'id' => '0d5a8fdd-ba93-4882-99cd-937d6cc50a67',
                'title' => 'Hygiene Promotion Toolkit for Community Health Workers',
                'slug' => 'hygiene-promotion-toolkit',
                'description' => 'A comprehensive guide for CHWs to conduct effective hygiene promotion activities in rural and urban communities.',
                'pillar_id' => $washPillarId,
                'category_id' => $communityCategoryId,
                'document_type_id' => $trainingTypeId,
                'publication_year' => '2021',
                'image_url' => 'https://images.unsplash.com/photo-1584744982491-665216d95f8b',
                'file_url' => '/uploads/resources/hygiene-toolkit.zip',
                'file_size' => '5.2MB',
                'file_type' => 'ZIP',
                'download_count' => 210,
                'view_count' => 892
            ]
        ];
        
        foreach ($resources as $resource) {
            $this->createResource($resource);
        }
    }
    
    protected function createResource(array $data)
    {
        $db = \Config\Database::connect();
        
        // Check if resource already exists
        $exists = $db->table('resources')
                   ->where('slug', $data['slug'])
                   ->countAllResults();
        
        if (!$exists) {
            $resourceData = [
                'id' => Uuid::uuid4()->toString(),
                'title' => $data['title'],
                'slug' => $data['slug'],
                'description' => $data['description'],
                'pillar_id' => $data['pillar_id'],
                'category_id' => $data['category_id'] ?? null,
                'document_type_id' => $data['document_type_id'],
                'publication_year' => $data['publication_year'],
                'image_url' => $data['image_url'] ?? null,
                'file_url' => $data['file_url'] ?? null,
                'file_size' => $data['file_size'] ?? null,
                'file_type' => $data['file_type'] ?? null,
                'is_featured' => $data['is_featured'] ?? 0,
                'is_published' => 1,
                'download_count' => $data['download_count'] ?? 0,
                'view_count' => $data['view_count'] ?? 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $db->table('resources')->insert($resourceData);
        }
    }
}