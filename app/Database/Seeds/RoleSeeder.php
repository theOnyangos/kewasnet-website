<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\RoleModel as Model;
use Carbon\Carbon;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Create RoleModel instance
        $model = new Model();

        // Role data
        $data = [
            [
                'id' => 'role-admin-0000-0000-000000000001',
                'role_name' => 'Administrator',
                'role_description' => 'Administrator role',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 'role-user--0000-0000-000000000002',
                'role_name' => 'User',
                'role_description' => 'User role',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 'role-emplo-0000-0000-000000000003',
                'role_name' => 'Employee',
                'role_description' => 'Employee role',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // Insert role data
        $model->insertBatch($data);
    }
}
