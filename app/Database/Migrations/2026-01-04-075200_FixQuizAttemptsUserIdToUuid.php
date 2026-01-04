<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixQuizAttemptsUserIdToUuid extends Migration
{
    public function up()
    {
        // First, let's check if there are any existing records
        // If there are, we need to handle them carefully
        
        // Modify user_id column from INT to VARCHAR(36) to store UUIDs
        $fields = [
            'user_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => false,
            ],
        ];
        
        $this->forge->modifyColumn('quiz_attempts', $fields);
    }

    public function down()
    {
        // Revert back to INT (not recommended if you have UUID data)
        $fields = [
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
        ];
        
        $this->forge->modifyColumn('quiz_attempts', $fields);
    }
}
