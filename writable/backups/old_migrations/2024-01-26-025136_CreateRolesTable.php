<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRolesTable extends Migration
{
    public function up()
{
    // Create roles table
    $this->forge->addField([
        'role_id' => [
            'type' => 'INT',
            'constraint' => 11,
            'auto_increment' => TRUE
        ],
        'role_name' => [
            'type' => 'VARCHAR',
            'constraint' => 100
        ],
        'role_description' => [
            'type' => 'TEXT',
            'null' => TRUE
        ],
        'created_at' => [
            'type' => 'DATETIME',
            'null' => TRUE,
            'default' => null,
        ],
        'updated_at' => [
            'type' => 'DATETIME',
            'null' => TRUE,
            'default' => null,
        ],
        'deleted_at' => [
            'type' => 'DATETIME',
            'null' => TRUE,
            'default' => null,
        ]
    ]);

    $this->forge->addKey('role_id', TRUE);
    $this->forge->createTable('roles');
}

    public function down()
    {
        // Drop the roles table
        $this->forge->dropTable('roles');
    }
}
