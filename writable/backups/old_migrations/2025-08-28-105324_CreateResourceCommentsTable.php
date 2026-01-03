<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateResourceCommentsTable extends Migration
{
    public function up()
    {
        // Drop table if it exists to avoid conflicts
        if ($this->db->tableExists('resource_comments')) {
            $this->forge->dropTable('resource_comments', true);
        }
        
        $this->forge->addField([
            'id' => [
                'type'           => 'VARCHAR',
                'constraint'     => 36,
                'null'           => false,
            ],
            'resource_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => false,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'parent_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
                'comment'    => 'For replies to comments',
            ],
            'content' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'is_approved' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'comment'    => '1=approved, 0=pending moderation',
            ],
            'helpful_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['resource_id', 'user_id']);
        $this->forge->addKey('parent_id');
        $this->forge->addKey('created_at');
        
        // Note: Foreign keys added after table creation to avoid constraint issues
        $this->forge->createTable('resource_comments');
        
        // Add foreign keys after table is created
        try {
            if ($this->db->tableExists('resources')) {
                $this->db->query("ALTER TABLE `resource_comments` ADD CONSTRAINT `fk_resource_comments_resource` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
            }
        } catch (\Exception $e) {
            // Foreign key may already exist - ignore
        }
        
        try {
            if ($this->db->tableExists('users')) {
                $this->db->query("ALTER TABLE `resource_comments` ADD CONSTRAINT `fk_resource_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
            }
        } catch (\Exception $e) {
            // Foreign key may already exist - ignore
        }
        
        try {
            // Self-referencing foreign key for parent_id
            $this->db->query("ALTER TABLE `resource_comments` ADD CONSTRAINT `fk_resource_comments_parent` FOREIGN KEY (`parent_id`) REFERENCES `resource_comments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
        } catch (\Exception $e) {
            // Foreign key may already exist - ignore
        }
    }

    public function down()
    {
        $this->forge->dropTable('resource_comments');
    }
}
