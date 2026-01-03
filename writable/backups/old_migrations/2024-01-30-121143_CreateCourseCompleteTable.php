<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCourseCompleteTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'ccompl_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'ccompl_student_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'ccompl_course_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'ccompl_status' => [
                'type' => 'ENUM',
                'constraint' => ['completed', 'in_progress', 'not_started'],
                'default' => 'not_started',
            ],
            'ccompl_completed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'ccompl_created_at' => [
                'type' => 'DATETIME',
            ],
            'ccompl_updated_at' => [
                'type' => 'DATETIME',
            ],
            'ccompl_deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('ccompl_id', true);
        $this->forge->createTable('course_completions');
    }

    public function down()
    {
        $this->forge->dropTable('course_completions');
    }
}