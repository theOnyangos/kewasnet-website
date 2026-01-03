<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\EmailSettings;
use Carbon\Carbon;

class EmailSettingsSeeder extends Seeder
{
    public function run()
    {
        //
        $data = [
            'host' => 'sandbox.smtp.mailtrap.io',
            'username' => '765c0fb44eb245',
            'password' => '9fae41180c3621',
            'encryption' => 'TLS',
            'port' => 2525,
            'from_address' => 'info@spinwingame.com',
            'from_name' => 'Spin Win Game',
            'updated_at' => Carbon::now(),
        ];

        $emailSettings = new EmailSettings();
        $emailSettings->insert($data);
    }
}
