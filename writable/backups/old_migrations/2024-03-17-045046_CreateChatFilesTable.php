<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChatFilesTable extends Migration
{
    public function up()
    {
        // Create chat files table
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'chat_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true
            ],
            'topic_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true
            ],
            'user_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true
            ],
            'file_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'file_type' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'file_size' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addKey('id', true);
        // $this->forge->addForeignKey('chat_id', 'chats', 'id', 'CASCADE', 'CASCADE');
        // $this->forge->addForeignKey('topic_id', 'chat_topics', 'id', 'CASCADE', 'CASCADE');
        // $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('chat_files');
    }

    public function down()
    {
        // Drop the chat files table
        $this->forge->dropTable('chat_files');
    }
}
