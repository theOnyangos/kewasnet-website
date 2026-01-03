<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PillarLinksSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'pillar_id' => 1,
                'links' => 'https://www.youtube.com/watch?v=ZT7LMGtK_2c',
            ],
            [
                'pillar_id' => 1,
                'links' => 'https://www.youtube.com/watch?v=t4DTpol1H04',
            ],
            [
                'pillar_id' => 2,
                'links' => 'https://www.youtube.com/watch?v=3Fcld4hr3og',
            ],


        ];
        $this->db->table('pillar_links')->insertBatch($data);
    }
}
