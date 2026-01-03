<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\PaystackSetting;

class PaystackSettingSeeder extends Seeder
{
    public function run()
    {
        // Create a new PaystackSetting instance
        $paystackSetting = new PaystackSetting();

        // Seed the paystack settings table
        $paystackSetting->insert([
            'public_key' => 'pk_test_9c79c48824b3ad364c99e197cee566e2c0f84afe',
            'secret_key' => 'sk_test_d80891834ac52efe752067bf957a46cb72ee9180',
            'payment_url' => 'https://api.paystack.co',
            'status' => 1,
        ]);
    }
}
