<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\GoogleSettings;
use Carbon\Carbon;

class GoogleSettingsSeeder extends Seeder
{
    public function run()
    {
        // Use environment variables for sensitive data
        $data = [
            'client_id' => getenv('GOOGLE_CLIENT_ID') ?: 'your-client-id-here',
            'client_secret' => getenv('GOOGLE_CLIENT_SECRET') ?: 'your-client-secret-here',
            'redirect_uri' => getenv('GOOGLE_REDIRECT_URI') ?: base_url('admin/login'),
            'application_name' => getenv('GOOGLE_APPLICATION_NAME') ?: 'KEWASNET Web Application',
            'created_at' => Carbon::now(),
        ];

        $model = new GoogleSettings();
        $model->insert($data);
    }
}
