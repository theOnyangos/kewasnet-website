<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

// Migration 6: Create Replies Table
class CreateRepliesTable extends Migration
{
    public function up()
    {
        // Drop table if it exists to avoid conflicts
        if ($this->db->tableExists('replies')) {
            $this->forge->dropTable('replies', true);
        }
        
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'primary' => true,
                'default' => 'UUID()',
                'null' => false,
            ],
            'discussion_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'user_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'parent_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => true, // For nested replies
            ],
            'content' => [
                'type' => 'LONGTEXT',
                'null' => false,
            ],
            'like_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'is_best_answer' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'hidden', 'reported', 'deleted'],
                'default' => 'active',
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
        $this->forge->addKey(['discussion_id', 'status', 'created_at']);
        $this->forge->addKey(['user_id', 'created_at']);
        $this->forge->addKey('parent_id');
        $this->forge->addKey('is_best_answer');
        // Note: Foreign keys added after table creation to avoid constraint issues
        $this->forge->createTable('replies');
        
        // Add foreign keys after table is created
        try {
            if ($this->db->tableExists('discussions')) {
                $this->db->query("ALTER TABLE `replies` ADD CONSTRAINT `fk_replies_discussion` FOREIGN KEY (`discussion_id`) REFERENCES `discussions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
            }
        } catch (\Exception $e) {
            // Foreign key may already exist - ignore
        }
        
        try {
            if ($this->db->tableExists('users')) {
                $this->db->query("ALTER TABLE `replies` ADD CONSTRAINT `fk_replies_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
            }
        } catch (\Exception $e) {
            // Foreign key may already exist - ignore
        }
        
        try {
            // Self-referencing foreign key for parent_id
            $this->db->query("ALTER TABLE `replies` ADD CONSTRAINT `fk_replies_parent` FOREIGN KEY (`parent_id`) REFERENCES `replies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
        } catch (\Exception $e) {
            // Foreign key may already exist - ignore
        }
    }

    public function down()
    {
        $this->forge->dropTable('replies');
    }
}
