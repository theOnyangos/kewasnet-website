<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\SocialLink as Model;

class SocialLinkSeeder extends Seeder
{
    public function run()
    {
        // Create the default social links
        $data = [
            'uuid' => 'social-l-0000-0000-000000000001',
            'facebook' => 'https://facebook.com',
            'twitter' => 'https://twitter.com',
            'instagram' => 'https://instagram.com',
            'linkedin' => 'https://linkedin.com',
            'youtube' => 'https://youtube.com',
        ];

        $model = new Model();
        $existing = $model->where('uuid', $data['uuid'])->first();

        if ($existing) {
            $model->update($data['uuid'], $data);
            return;
        }

        $model->insert($data);
    }
}
