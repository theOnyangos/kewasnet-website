<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAnswersTable extends Migration
{
    public function up()
    {
        // Create the answers table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'question_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'answer_text' => [
                'type' => 'TEXT',
            ],
            'is_correct' => [
                'type' => 'ENUM',
                'constraint' => ['true', 'false'],
                'null' => true,
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
        // $this->forge->addForeignKey('question_id', 'questions', 'question_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('answers');
    }

    public function down()
    {
        // Drop the table
        $this->forge->dropTable('answers');
    }
}
