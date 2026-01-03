<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserSelectedAnswersTable extends Migration
{
    public function up()
    {
        // Create the user selected answers table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'question_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'selected_answer_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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
        // $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        // $this->forge->addForeignKey('question_id', 'questions', 'id', 'CASCADE', 'CASCADE');
        // $this->forge->addForeignKey('selected_answer_id', 'answers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_selected_answers');
    }

    public function down()
    {
        // Drop the table
        $this->forge->dropTable('user_selected_answers');
    }
}
