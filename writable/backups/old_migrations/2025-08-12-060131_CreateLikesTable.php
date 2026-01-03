<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

// Migration 8: Create Likes Table
class CreateLikesTable extends Migration
{
    public function up()
    {
        // Drop table if it exists to avoid conflicts
        if ($this->db->tableExists('likes')) {
            $this->forge->dropTable('likes', true);
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
            'likeable_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false, // 'discussion' or 'reply'
            ],
            'likeable_id' => [
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
        $this->forge->addUniqueKey(['user_id', 'likeable_type', 'likeable_id']);
        $this->forge->addKey(['likeable_type', 'likeable_id']);
        // Note: Foreign key added after table creation to avoid constraint issues
        $this->forge->createTable('likes');
        
        // Add foreign key after table is created
        try {
            if ($this->db->tableExists('users')) {
                $this->db->query("ALTER TABLE `likes` ADD CONSTRAINT `fk_likes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
            }
        } catch (\Exception $e) {
            // Foreign key may already exist - ignore
        }
    }

    public function down()
    {
        $this->forge->dropTable('likes');
    }
}
