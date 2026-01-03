<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobApplicationTable extends Migration
{
    public function up()
    {
        // Define table fields
        $fields = [
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'opportunity_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'cv_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'cover_letter' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'career_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ];

        // Add fields to Forge
        $this->forge->addField($fields);
        $this->forge->addKey('id', true); // Primary key
        $this->forge->createTable('job_applications', true);
    }

    public function down()
    {
        // Drop the table if exists
        $this->forge->dropTable('job_applications', true);
    }
}
