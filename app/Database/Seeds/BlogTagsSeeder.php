<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;

class BlogTagsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => '3ef0205d-f04b-4dcc-9e72-59d1f39a1089',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'WASH',
                'slug' => 'wash',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 'ec4972e3-decf-4845-a8c9-08274da5b324',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Kenya',
                'slug' => 'kenya',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => '9738ea5f-047c-46d9-9144-d112ad9f8d43',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Sustainability',
                'slug' => 'sustainability',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => '593c28e1-d7c9-491b-b998-49be12adbf5d',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Climate',
                'slug' => 'climate',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 'c00ae2bf-4efd-4aaf-b63f-cafe162c114e',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'SDG6',
                'slug' => 'sdg6',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 'd336ecc6-651c-4aea-b9ad-d6fc39681f75',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Water',
                'slug' => 'water',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => '2b1579a6-d3ab-4457-8c82-55172f5190f1',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Sanitation',
                'slug' => 'sanitation',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 'd99e16bd-2963-4dec-b965-62c38f5b35f3',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Hygiene',
                'slug' => 'hygiene',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('blog_tags')->insertBatch($data);
    }
}