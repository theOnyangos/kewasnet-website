<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAIDailyUsageTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'comment' => 'UUID primary key',
            ],
            'usage_date' => [
                'type' => 'DATE',
                'comment' => 'Date bucket (server date)',
            ],
            'user_type' => [
                'type' => 'ENUM',
                'constraint' => ['customer', 'admin'],
                'default' => 'customer',
                'comment' => 'User type',
            ],
            'identity_type' => [
                'type' => 'ENUM',
                'constraint' => ['user', 'session', 'ip'],
                'comment' => 'Identity key type',
            ],
            'identity' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'Identity key value (user_id/session_id/ip)',
            ],
            'message_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 0,
                'comment' => 'Messages sent in the day bucket',
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
        $this->forge->addKey('usage_date');
        $this->forge->addKey('user_type');
        $this->forge->addKey('identity_type');
        $this->forge->addKey('identity');
        $this->forge->addUniqueKey(['usage_date', 'user_type', 'identity_type', 'identity'], 'ai_daily_usage_unique');

        $this->forge->createTable('ai_daily_usage');
    }

    public function down()
    {
        $this->forge->dropTable('ai_daily_usage', true);
    }
}

