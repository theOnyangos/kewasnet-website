<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\MpesaSettings;
use Carbon\Carbon;

class MpesaSettingsSeeder extends Seeder
{
    public function run()
    {
        // Mpesa settings data
        $data = [
            'consumer_key' => 'Ma7I4tMaRYbtVGorPWkYHuq2vWkoKfKA',
            'consumer_secret' => 'EAAoPehC5z2YvS9F',
            'business_short_code' => '4113409',
            'pass_key' => 'd382f46466860dafe8b8f7632154d9c2b13419e8071594f8b282e39252052d93',
            'account_reference' => 'spintowinkenya',
            'party_b' => '4113409',
            'register_url_endpoint' => '/mpesa/register',
            'stk_push_endpoint' => '/mpesa/stkpush',
            'callback_endpoint' => '/mpesa/callback',
            'test_mode' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];

        // Using Query Builder
        $mpesaSettings = new MpesaSettings();
        $mpesaSettings->insert($data);
    }
}
