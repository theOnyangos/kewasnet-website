<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPassingScoreAndAttemptsToQuizzes extends Migration
{
    public function up()
    {
        $fields = [
            'passing_score' => [
                'type' => 'INT',
                'constraint' => 3,
                'default' => 70,
                'comment' => 'Passing score percentage (0-100)',
            ],
            'max_attempts' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'Maximum number of attempts allowed (null for unlimited)',
            ],
        ];
        
        $this->forge->addColumn('quizzes', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('quizzes', ['passing_score', 'max_attempts']);
    }
}
