<?php

// Migration 1: Create Forums Table
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateForumsTable extends Migration
{
    public function up()
    {
        // Drop table if it exists to avoid conflicts
        if ($this->db->tableExists('forums')) {
            $this->forge->dropTable('forums', true);
        }
        
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'primary' => true,
                'default' => 'UUID()',
                'null' => false,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'icon' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'color' => [
                'type' => 'VARCHAR',
                'constraint' => 7,
                'null' => true,
                'default' => '#3B82F6',
            ],
            'is_draft' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'is_active' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
            'sort_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'total_discussions' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'total_replies' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'last_activity_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey(['is_active', 'sort_order']);
        // Note: Foreign key added after table creation to avoid constraint issues
        $this->forge->createTable('forums');
        
        // Add foreign key after table is created (if users table exists)
        try {
            if ($this->db->tableExists('users')) {
                $this->db->query("ALTER TABLE `forums` ADD CONSTRAINT `fk_forums_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
            }
        } catch (\Exception $e) {
            // Foreign key may already exist or users table structure may differ - ignore
        }
    }

    public function down()
    {
        $this->forge->dropTable('forums');
    }
}
