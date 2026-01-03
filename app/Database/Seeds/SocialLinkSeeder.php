<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\SocialLink as Model;

class SocialLinkSeeder extends Seeder
{
    public function run()
    {
        // Create the default social links
        $model = new Model();
        $model->insert([
            'uuid' => 'social-l-0000-0000-000000000001',
            'facebook' => 'https://facebook.com',
            'twitter' => 'https://twitter.com',
            'instagram' => 'https://instagram.com',
            'linkedin' => 'https://linkedin.com',
            'youtube' => 'https://youtube.com',
        ]);
    }
}
