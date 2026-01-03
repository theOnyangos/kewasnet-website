<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PillarCategorySeeder extends Seeder
{
    public function run()
    {
        $data = [
        [
            'name' => 'Innovation',
            'slug' => 'innovation',
        ],

        [
            'name' => 'Integrity',
            'slug' => 'integrity',
        ],
        [
            'name' => 'Professionalism',
            'slug' => 'professionalism',
        ],
        [
            'name' => 'Accountability',
            'slug' => 'accountability',
        ]
    ];
    $this->db->table('pillar_categories')->insertBatch($data);
    }
}
