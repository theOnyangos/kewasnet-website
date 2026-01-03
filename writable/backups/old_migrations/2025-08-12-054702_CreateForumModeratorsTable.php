<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

// Migration 2: Create Forum Moderators Table (Pivot)
class CreateForumModeratorsTable extends Migration
{
    public function up()
    {
        // Drop table if it exists to avoid conflicts
        if ($this->db->tableExists('forum_moderators')) {
            $this->forge->dropTable('forum_moderators', true);
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
            'moderator_title' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'assigned_by' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'assigned_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'is_active' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['forum_id', 'user_id']);
        $this->forge->addKey(['forum_id', 'is_active']);
        // Note: Foreign keys added after table creation to avoid constraint issues
        $this->forge->createTable('forum_moderators');
        
        // Add foreign keys after table is created
        try {
            if ($this->db->tableExists('forums')) {
                $this->db->query("ALTER TABLE `forum_moderators` ADD CONSTRAINT `fk_forum_moderators_forum` FOREIGN KEY (`forum_id`) REFERENCES `forums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
            }
        } catch (\Exception $e) {
            // Foreign key may already exist - ignore
        }
        
        try {
            if ($this->db->tableExists('users')) {
                $this->db->query("ALTER TABLE `forum_moderators` ADD CONSTRAINT `fk_forum_moderators_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
                $this->db->query("ALTER TABLE `forum_moderators` ADD CONSTRAINT `fk_forum_moderators_assigned_by` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
            }
        } catch (\Exception $e) {
            // Foreign key may already exist - ignore
        }
    }

    public function down()
    {
        $this->forge->dropTable('forum_moderators');
    }
}
