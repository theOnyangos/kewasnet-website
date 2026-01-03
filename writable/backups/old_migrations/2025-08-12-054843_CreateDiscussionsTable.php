<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

// Migration 3: Create Discussions Table
class CreateDiscussionsTable extends Migration
{
    public function up()
    {
        // Drop table if it exists to avoid conflicts
        if ($this->db->tableExists('discussions')) {
            $this->forge->dropTable('discussions', true);
        }
        
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'primary' => true,
                'default' => 'UUID()',
                'null' => false,
            ],
            'forum_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'user_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => false,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => false,
            ],
            'content' => [
                'type' => 'LONGTEXT',
                'null' => false,
            ],
            'tags' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'is_pinned' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'is_locked' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'is_featured' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'view_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'reply_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'like_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'last_reply_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'last_reply_by' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => true,
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
        $this->forge->addKey('slug');
        $this->forge->addKey(['forum_id', 'status', 'is_pinned', 'last_reply_at']);
        $this->forge->addKey(['user_id', 'created_at']);
        $this->forge->addKey('last_reply_at');
        // Note: Foreign keys added after table creation to avoid constraint issues
        $this->forge->createTable('discussions');
        
        // Add foreign keys after table is created
        try {
            if ($this->db->tableExists('forums')) {
                $this->db->query("ALTER TABLE `discussions` ADD CONSTRAINT `fk_discussions_forum` FOREIGN KEY (`forum_id`) REFERENCES `forums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
            }
        } catch (\Exception $e) {
            // Foreign key may already exist - ignore
        }
        
        try {
            if ($this->db->tableExists('users')) {
                $this->db->query("ALTER TABLE `discussions` ADD CONSTRAINT `fk_discussions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
                $this->db->query("ALTER TABLE `discussions` ADD CONSTRAINT `fk_discussions_last_reply_by` FOREIGN KEY (`last_reply_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE");
            }
        } catch (\Exception $e) {
            // Foreign key may already exist - ignore
        }
    }

    public function down()
    {
        $this->forge->dropTable('discussions');
    }
}
