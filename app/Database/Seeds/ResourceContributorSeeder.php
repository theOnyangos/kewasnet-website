<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;

class ResourceContributorSeeder extends Seeder
{
    public function run()
    {
        // Ensure we have resources and contributors to reference
        $this->call('ContributorSeeder');
        $this->call('ResourceSeeder'); // Assuming you have a ResourceSeeder
        
        // Get existing IDs
        $resourceIds = array_column(
            $this->db->table('resources')->select('id')->get()->getResultArray(),
            'id'
        );
        
        $contributorIds = array_column(
            $this->db->table('contributors')->select('id')->get()->getResultArray(),
            'id'
        );
        
        if (empty($resourceIds) || empty($contributorIds)) {
            echo "No resources or contributors found. Please seed those first.\n";
            return;
        }
        
        $faker = \Faker\Factory::create();
        $roles = ['Author', 'Editor', 'Reviewer', 'Translator', 'Illustrator'];
        
        $data = [];
        $existingPairs = [];
        
        // Create 20 resource-contributor relationships
        for ($i = 0; $i < 20; $i++) {
            $resourceId = $faker->randomElement($resourceIds);
            $contributorId = $faker->randomElement($contributorIds);
            
            $pairKey = "{$resourceId}-{$contributorId}";
            
            if (!isset($existingPairs[$pairKey])) {
                $data[] = [
                    'id' => Uuid::uuid4()->toString(),
                    'resource_id' => $resourceId,
                    'contributor_id' => $contributorId,
                    'role' => $faker->randomElement($roles),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $existingPairs[$pairKey] = true;
            }
        }
        
        // Using Query Builder
        $this->db->table('resource_contributors')->insertBatch($data);
    }
}