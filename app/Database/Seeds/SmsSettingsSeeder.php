<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\SmsSetting;
use Carbon\Carbon;

class SmsSettingsSeeder extends Seeder
{
    public function run()
    {
        // Sms Setting Data
        $data = [
            'api_endpoint' => 'https://sms.textsms.co.ke/api/services/sendsms/',
            'api_key' => 'afc289bf1ea88ab2feff6126c1686a5e',
            'short_code' => 'TextSMS',
            'token' => 'afc289bf1ea88ab2feff6126c1686a5e',
            'partner_id' => 7739,
            'created_at' => Carbon::now(),
        ];

        // Add settings to Sms Settings
        $smsSetting = new SmsSetting();
        $smsSetting->insert($data);
    }
}
