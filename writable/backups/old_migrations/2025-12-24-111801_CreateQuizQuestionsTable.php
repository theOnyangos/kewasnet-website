<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuizQuestionsTable extends Migration
{
    public function up()
    {
        // Check if table already exists
        if ($this->db->tableExists('quiz_questions')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'quiz_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'question_text' => [
                'type' => 'TEXT',
            ],
            'question_type' => [
                'type' => 'ENUM',
                'constraint' => ['multiple_choice', 'true_false', 'short_answer'],
                'default' => 'multiple_choice',
            ],
            'points' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
            ],
            'order_index' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
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

        $this->forge->addKey('id', true);
        $this->forge->addKey('quiz_id');
        $this->forge->createTable('quiz_questions');
    }

    public function down()
    {
        $this->forge->dropTable('quiz_questions');
    }
}
