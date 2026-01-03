<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuizQuestionOptionsTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('quiz_question_options')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'question_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'option_text' => [
                'type' => 'TEXT',
            ],
            'is_correct' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
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
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('question_id');
        $this->forge->createTable('quiz_question_options');
    }

    public function down()
    {
        $this->forge->dropTable('quiz_question_options');
    }
}
