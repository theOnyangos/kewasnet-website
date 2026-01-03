<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

// Migration 9: Create Bookmarks Table
class CreateBookmarksTable extends Migration
{
    public function up()
    {
        // Drop table if it exists to avoid conflicts
        if ($this->db->tableExists('bookmarks')) {
            $this->forge->dropTable('bookmarks', true);
        }
        
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'primary' => true,
                'default' => 'UUID()',
                'null' => false,
            ],
            'user_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'discussion_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['user_id', 'discussion_id']);
        $this->forge->addKey('discussion_id');
        // Note: Foreign keys added after table creation to avoid constraint issues
        $this->forge->createTable('bookmarks');
        
        // Add foreign keys after table is created
        try {
            if ($this->db->tableExists('users')) {
                $this->db->query("ALTER TABLE `bookmarks` ADD CONSTRAINT `fk_bookmarks_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
            }
        } catch (\Exception $e) {
            // Foreign key may already exist - ignore
        }
        
        try {
            if ($this->db->tableExists('discussions')) {
                $this->db->query("ALTER TABLE `bookmarks` ADD CONSTRAINT `fk_bookmarks_discussion` FOREIGN KEY (`discussion_id`) REFERENCES `discussions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
            }
        } catch (\Exception $e) {
            // Foreign key may already exist - ignore
        }
    }

    public function down()
    {
        $this->forge->dropTable('bookmarks');
    }
}
