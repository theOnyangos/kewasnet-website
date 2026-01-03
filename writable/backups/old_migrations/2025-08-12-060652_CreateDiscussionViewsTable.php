<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

// Migration 11: Create Discussion Views Table (for tracking unique views)
class CreateDiscussionViewsTable extends Migration
{
    public function up()
    {
        // Drop table if it exists to avoid conflicts
        if ($this->db->tableExists('discussion_views')) {
            $this->forge->dropTable('discussion_views', true);
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
                'null' => true, // Can be null for guest views
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'viewed_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['discussion_id', 'viewed_at']);
        $this->forge->addKey('user_id');
        $this->forge->addKey('ip_address');
        // Note: Foreign keys added after table creation to avoid constraint issues
        $this->forge->createTable('discussion_views');
        
        // Add foreign keys after table is created
        try {
            if ($this->db->tableExists('discussions')) {
                $this->db->query("ALTER TABLE `discussion_views` ADD CONSTRAINT `fk_discussion_views_discussion` FOREIGN KEY (`discussion_id`) REFERENCES `discussions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
            }
        } catch (\Exception $e) {
            // Foreign key may already exist - ignore
        }
        
        try {
            if ($this->db->tableExists('users')) {
                $this->db->query("ALTER TABLE `discussion_views` ADD CONSTRAINT `fk_discussion_views_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
            }
        } catch (\Exception $e) {
            // Foreign key may already exist - ignore
        }
    }

    public function down()
    {
        $this->forge->dropTable('discussion_views');
    }
}
