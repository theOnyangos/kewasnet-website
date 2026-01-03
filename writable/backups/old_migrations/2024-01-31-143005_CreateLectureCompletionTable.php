<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLectureCompletionTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'lcompl_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'lcompl_student_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'lcompl_course_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'lcompl_status' => [
                'type' => 'ENUM',
                'constraint' => ['completed', 'in_progress', 'not_started'],
                'default' => 'not_started',
            ],
            'lcompl_lecture_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'lcompl_completed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'lcompl_created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'lcompl_updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'lcompl_deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('lcompl_id');
        $this->forge->createTable('lecture_completions');
    }

    public function down()
    {
        $this->forge->dropTable('lecture_completions');
    }
}
