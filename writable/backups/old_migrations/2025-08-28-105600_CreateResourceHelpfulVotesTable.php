<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateResourceHelpfulVotesTable extends Migration
{
    public function up()
    {
        // Drop table if it exists to avoid conflicts
        if ($this->db->tableExists('resource_helpful_votes')) {
            $this->forge->dropTable('resource_helpful_votes', true);
        }
        
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'resource_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
                'comment'    => 'If voting on a resource',
            ],
            'comment_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
                'comment'    => 'If voting on a comment',
            ],
            'vote_type' => [
                'type'       => 'ENUM',
                'constraint' => ['helpful', 'not_helpful'],
                'default'    => 'helpful',
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
        $this->forge->addKey(['user_id', 'resource_id', 'comment_id'], false, 'unique_vote');
        $this->forge->addKey('resource_id');
        $this->forge->addKey('comment_id');
        
        // Note: Foreign keys added after table creation to avoid constraint issues
        $this->forge->createTable('resource_helpful_votes');
        
        // Add foreign keys after table is created
        try {
            if ($this->db->tableExists('users')) {
                $this->db->query("ALTER TABLE `resource_helpful_votes` ADD CONSTRAINT `fk_resource_helpful_votes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
            }
        } catch (\Exception $e) {
            // Foreign key may already exist - ignore
        }
        
        try {
            if ($this->db->tableExists('resources')) {
                $this->db->query("ALTER TABLE `resource_helpful_votes` ADD CONSTRAINT `fk_resource_helpful_votes_resource` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
            }
        } catch (\Exception $e) {
            // Foreign key may already exist - ignore
        }
        
        try {
            if ($this->db->tableExists('resource_comments')) {
                $this->db->query("ALTER TABLE `resource_helpful_votes` ADD CONSTRAINT `fk_resource_helpful_votes_comment` FOREIGN KEY (`comment_id`) REFERENCES `resource_comments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
            }
        } catch (\Exception $e) {
            // Foreign key may already exist - ignore
        }
    }

    public function down()
    {
        $this->forge->dropTable('resource_helpful_votes');
    }
}
