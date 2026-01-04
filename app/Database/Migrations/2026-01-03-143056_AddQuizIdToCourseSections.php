<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddQuizIdToCourseSections extends Migration
{
    public function up()
    {
        $this->forge->addColumn('course_sections', [
            'quiz_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
                'after'      => 'status'
            ]
        ]);
        
        // Add foreign key constraint
        $this->forge->addForeignKey('quiz_id', 'quizzes', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        // Drop foreign key first
        $this->forge->dropForeignKey('course_sections', 'course_sections_quiz_id_foreign');
        
        // Drop column
        $this->forge->dropColumn('course_sections', 'quiz_id');
    }
}
