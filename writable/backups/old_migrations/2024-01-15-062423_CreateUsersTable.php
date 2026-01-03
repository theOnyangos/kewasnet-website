<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        // Create users
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'role_id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
            ],
            'registered_by' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'null'           => true,
            ],
            'employee_number' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
                'null'           => true,
            ],
            'first_name' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
            ],
            'last_name' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
            ],
            'username' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
            ],
            'email' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
            ],
            'picture' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
                'null'           => true,
            ],
            'phone' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
                'null'           => true,
            ],
            'bio' => [
                'type'           => 'TEXT',
                'null'           => true,
            ],
            'profile_cover_image' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
                'null'           => true,
            ],
            'status' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
                'default'        => 'active',
            ],
            'account_status'     => [
                'type'           => 'ENUM',
                'constraint'     => ['active', 'suspended', 'blocked'],
                'default'        => 'active',
            ],
            'email_verified_at' => [
                'type'          => 'DATETIME',
                'null'          => true,
            ],
            'terms'             => [
                'type'          => 'BOOLEAN',
                'default'       => false,
            ],
            'password' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
            ],
            'created_at' => [
                'type'          => 'DATETIME',
                'null'          => true,
            ],
            'updated_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
            'deleted_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('users');
    }

    public function down()
    {
        // remove users table
        $this->forge->dropTable('users');
    }
}
