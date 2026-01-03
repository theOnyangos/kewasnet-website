<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $data = [

            [
                'name' => 'Water',
                'slug' => 'water',
            ],

            [
                'name' => 'Sanitation',
                'slug' => 'sanitation',
            ]
        ];
        $this->db->table('blog_categories')->insertBatch($data);
    }
}
