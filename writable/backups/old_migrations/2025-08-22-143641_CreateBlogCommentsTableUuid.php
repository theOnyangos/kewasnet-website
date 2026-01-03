<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBlogCommentsTableUuid extends Migration
{
    public function up()
    {
        // Drop table if it exists to avoid conflicts
        if ($this->db->tableExists('blog_comments')) {
            $this->forge->dropTable('blog_comments', true);
        }
        
        // Create the blog_comments table with UUID support
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'primary' => true,
                'null' => false,
            ],
            'blog_post_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'user_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => true, // Allow anonymous comments
            ],
            'parent_comment_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => true, // For reply functionality
            ],
            'author_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true, // For anonymous comments
            ],
            'author_email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true, // For anonymous comments
            ],
            'content' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected', 'spam'],
                'default' => 'pending',
            ],
            'comment_type' => [
                'type' => 'ENUM',
                'constraint' => ['comment', 'reply'],
                'default' => 'comment',
            ],
            'author_type' => [
                'type' => 'ENUM',
                'constraint' => ['registered', 'anonymous'],
                'default' => 'anonymous',
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
            'is_featured' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'likes_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'replies_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
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

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['blog_post_id']);
        $this->forge->addKey(['user_id']);
        $this->forge->addKey(['parent_comment_id']);
        $this->forge->addKey(['status']);
        $this->forge->addKey(['created_at']);

        // Note: Foreign keys added after table creation to avoid constraint issues
        $this->forge->createTable('blog_comments');
        
        // Add foreign keys after table is created
        try {
            if ($this->db->tableExists('blog_posts')) {
                $this->db->query("ALTER TABLE `blog_comments` ADD CONSTRAINT `fk_blog_comments_post` FOREIGN KEY (`blog_post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
            }
        } catch (\Exception $e) {
            // Foreign key may already exist - ignore
        }
        
        try {
            if ($this->db->tableExists('users')) {
                $this->db->query("ALTER TABLE `blog_comments` ADD CONSTRAINT `fk_blog_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL");
            }
        } catch (\Exception $e) {
            // Foreign key may already exist - ignore
        }
        
        try {
            // Self-referencing foreign key for parent_comment_id
            $this->db->query("ALTER TABLE `blog_comments` ADD CONSTRAINT `fk_blog_comments_parent` FOREIGN KEY (`parent_comment_id`) REFERENCES `blog_comments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
        } catch (\Exception $e) {
            // Foreign key may already exist - ignore
        }
    }

    public function down()
    {
        // Drop the blog_comments table
        $this->forge->dropTable('blog_comments');
    }
}
