<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAITables extends Migration
{
    public function up()
    {
        // AI Conversations table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'comment' => 'UUID primary key',
            ],
            'user_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => true,
                'comment' => 'User ID for authenticated users (references system_users.id)',
            ],
            'session_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Session ID for anonymous users',
            ],
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['customer', 'admin'],
                'default' => 'customer',
                'comment' => 'Conversation type',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'archived'],
                'default' => 'active',
                'comment' => 'Conversation status',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Created timestamp',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Updated timestamp',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('session_id');
        $this->forge->addKey('type');
        $this->forge->createTable('ai_conversations');

        // AI Messages table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'comment' => 'UUID primary key',
            ],
            'conversation_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'comment' => 'Foreign key to ai_conversations.id',
            ],
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['user', 'assistant', 'system'],
                'comment' => 'Message role',
            ],
            'content' => [
                'type' => 'TEXT',
                'comment' => 'Message content',
            ],
            'metadata' => [
                'type' => 'JSON',
                'null' => true,
                'comment' => 'Additional metadata (token usage, model used, etc.)',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Created timestamp',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('conversation_id');
        $this->forge->addKey('created_at');
        $this->forge->createTable('ai_messages');

        // AI Agent Settings table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'comment' => 'UUID primary key',
            ],
            'setting_key' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'Setting key name',
            ],
            'setting_value' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Setting value (can be JSON)',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Setting description',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Updated timestamp',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('setting_key');
        $this->forge->createTable('ai_agent_settings');
    }

    public function down()
    {
        $this->forge->dropTable('ai_messages', true);
        $this->forge->dropTable('ai_conversations', true);
        $this->forge->dropTable('ai_agent_settings', true);
    }
}
