<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;

class ContributorSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();
        
        $data = [];
        
        for ($i = 0; $i < 10; $i++) {
            $data[] = [
                'id' => Uuid::uuid4()->toString(),
                'name' => $faker->name,
                'organization' => $faker->company,
                'email' => $faker->email,
                'photo_url' => $faker->imageUrl(200, 200, 'people'),
                'bio' => $faker->paragraph(3),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
        
        // Using Query Builder
        $this->db->table('contributors')->insertBatch($data);
    }
}