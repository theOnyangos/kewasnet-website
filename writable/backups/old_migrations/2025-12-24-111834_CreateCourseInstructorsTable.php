<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCourseInstructorsTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('course_instructors')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'course_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'instructor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['primary', 'assistant'],
                'default' => 'primary',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('course_id');
        $this->forge->addKey('instructor_id');
        $this->forge->createTable('course_instructors');
    }

    public function down()
    {
        $this->forge->dropTable('course_instructors');
    }
}
