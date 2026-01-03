<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\FacebookSettings;
use Carbon\Carbon;

class FacebookSettingsSeeder extends Seeder
{
    public function run()
    {
        // Create a new FacebookSettings model
        $facebookSettingsModel = new FacebookSettings();

        // Create a new FacebookSettings object
        $settingsObject = array(
            'page_id' => '',
            'page_access_token' => '',
            'app_id' => '',
            'app_secret' => '',
            'verification_token' => '',
            'webhook_url' => '',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        );

        // Insert the FacebookSettings object to database
        $facebookSettingsModel->insert($settingsObject);
    }
}
