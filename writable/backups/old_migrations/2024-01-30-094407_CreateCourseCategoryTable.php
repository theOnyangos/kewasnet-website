<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCourseCategoryTable extends Migration
{
    public function up()
    {
        // Create the course categories table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
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
        ]);
        $this->forge->addKey('id');
        $this->forge->createTable('course_categories');
    }

    public function down()
    {
        // Drop the course categories table
        $this->forge->dropTable('course_categories');
    }
}
