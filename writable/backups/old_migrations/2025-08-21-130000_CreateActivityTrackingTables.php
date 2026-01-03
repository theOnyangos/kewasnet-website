<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateActivityTrackingTables extends Migration
{
    public function up()
    {
        // Drop tables if they exist to avoid conflicts
        $tables = ['user_sessions', 'page_views', 'user_events'];
        foreach ($tables as $table) {
            if ($this->db->tableExists($table)) {
                $this->forge->dropTable($table, true);
            }
        }
        
        // User Sessions Table
        $this->forge->addField([
            'id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
                'primary'    => true,
                'default'    => 'UUID()',
                'null'       => false,
            ],
            'session_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
                'null'       => false,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => false,
            ],
            'user_agent' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'browser' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'device' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'os' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'country' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'city' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'referrer' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'analytics_consent' => [
                'type'       => 'BOOLEAN',
                'default'    => false,
                'null'       => false,
            ],
            'marketing_consent' => [
                'type'       => 'BOOLEAN',
                'default'    => false,
                'null'       => false,
            ],
            'session_start' => [
                'type'       => 'DATETIME',
                'null'       => false,
            ],
            'session_end' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'total_duration' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'comment'    => 'Duration in seconds',
            ],
            'page_views' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'is_bounce' => [
                'type'       => 'BOOLEAN',
                'default'    => true,
                'null'       => false,
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'default'    => null,
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'default'    => null,
            ],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('session_id');
        $this->forge->addKey('user_id');
        $this->forge->addKey('ip_address');
        $this->forge->addKey('session_start');
        $this->forge->addKey('analytics_consent');
        $this->forge->createTable('user_sessions');

        // Page Views Table
        $this->forge->addField([
            'id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
                'primary'    => true,
                'default'    => 'UUID()',
                'null'       => false,
            ],
            'session_id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
                'null'       => false,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'page_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => false,
            ],
            'page_title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'page_category' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'time_on_page' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'comment'    => 'Time in seconds',
            ],
            'scroll_depth' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
                'comment'    => 'Percentage scrolled',
            ],
            'exit_page' => [
                'type'       => 'BOOLEAN',
                'default'    => false,
                'null'       => false,
            ],
            'viewed_at' => [
                'type'       => 'DATETIME',
                'null'       => false,
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'default'    => null,
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'default'    => null,
            ],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('session_id');
        $this->forge->addKey('user_id');
        $this->forge->addKey('page_url');
        $this->forge->addKey('page_category');
        $this->forge->addKey('viewed_at');
        // Note: Foreign key added after table creation to avoid constraint issues
        $this->forge->createTable('page_views');
        
        // Add foreign key after table is created
        try {
            if ($this->db->tableExists('user_sessions')) {
                $this->db->query("ALTER TABLE `page_views` ADD CONSTRAINT `fk_page_views_session` FOREIGN KEY (`session_id`) REFERENCES `user_sessions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
            }
        } catch (\Exception $e) {
            // Foreign key may already exist - ignore
        }

        // User Events Table
        $this->forge->addField([
            'id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
                'primary'    => true,
                'default'    => 'UUID()',
                'null'       => false,
            ],
            'session_id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
                'null'       => false,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'event_type' => [
                'type'       => 'ENUM',
                'constraint' => ['click', 'form_submit', 'download', 'search', 'registration', 'login', 'logout', 'contact', 'newsletter', 'custom'],
                'null'       => false,
            ],
            'event_category' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'event_action' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'event_label' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'event_value' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'page_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => false,
            ],
            'occurred_at' => [
                'type'       => 'DATETIME',
                'null'       => false,
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'default'    => null,
            ],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('session_id');
        $this->forge->addKey('user_id');
        $this->forge->addKey('event_type');
        $this->forge->addKey('event_category');
        $this->forge->addKey('occurred_at');
        // Note: Foreign key added after table creation to avoid constraint issues
        $this->forge->createTable('user_events');
        
        // Add foreign key after table is created
        try {
            if ($this->db->tableExists('user_sessions')) {
                $this->db->query("ALTER TABLE `user_events` ADD CONSTRAINT `fk_user_events_session` FOREIGN KEY (`session_id`) REFERENCES `user_sessions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
            }
        } catch (\Exception $e) {
            // Foreign key may already exist - ignore
        }
    }

    public function down()
    {
        $this->forge->dropTable('user_events');
        $this->forge->dropTable('page_views');
        $this->forge->dropTable('user_sessions');
    }
}
