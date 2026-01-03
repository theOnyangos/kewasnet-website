<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCareersTable extends Migration
{
    public function up()
    {
        // Define table fields
        $fields = [
            'co_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'co_position' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'co_type' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'co_department_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'co_period' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'co_application_instruction_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'co_image_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'co_description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'co_archived_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'co_created_at' => [
                'type' => 'DATETIME',
            ],
            'co_updated_at' => [
                'type' => 'DATETIME',
            ],
            'co_deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

        ];

        // Add fields to Forge
        $this->forge->addField($fields);
        $this->forge->addKey('co_id', true); // Primary key
        $this->forge->createTable('careers', true);
    }

    public function down()
    {
        // Drop the table if exists
        $this->forge->dropTable('careers', true);
    }
}
