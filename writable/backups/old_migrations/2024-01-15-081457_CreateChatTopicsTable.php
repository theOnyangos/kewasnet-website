<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChatTopicsTable extends Migration
{
    public function up()
    {
        // Create a chat topics table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'body' => [
                'type' => 'TEXT'
            ],
            'created_at' => [
                'type' => 'DATETIME'
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('chat_topics');
    }

    public function down()
    {
        // Drop the chat topics table
        $this->forge->dropTable('chat_topics');
    }
}
