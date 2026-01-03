<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateResourceTables extends Migration
{
    public function up()
    {
        // Document types table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'color' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => '#3b82f6',
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
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('document_types');

        // Resource categories table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'pillar_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addKey(['pillar_id', 'slug']);
        $this->forge->createTable('resource_categories');

        // Contributors table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'organization' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'photo_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'bio' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->createTable('contributors');

        // Resources table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'pillar_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'category_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => true,
            ],
            'document_type_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'content' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'image_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'file_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'file_size' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'file_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'publication_year' => [
                'type' => 'YEAR',
                'null' => true,
            ],
            'download_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'helpful_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 0,
            ],
            'is_private' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1=private (employees only), 0=public',
            ],
            'view_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'is_featured' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'is_published' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'meta_title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'meta_description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'null' => true,
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
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey('is_featured');
        $this->forge->addKey('is_published');
        $this->forge->addKey('publication_year');
        $this->forge->addKey('is_private');
        $this->forge->addKey('helpful_count');
        $this->forge->addKey('pillar_id');
        $this->forge->addKey('category_id');
        $this->forge->addKey('document_type_id');
        $this->forge->addForeignKey('category_id', 'resource_categories', 'id', 'SET NULL', 'SET NULL', 'resources_category_id_foreign');
        $this->forge->addForeignKey('document_type_id', 'document_types', 'id', '', '', 'resources_document_type_id_foreign');
        $this->forge->createTable('resources');

        // Resource comments table
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'resource_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'parent_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
                'comment' => 'For replies to comments',
            ],
            'content' => [
                'type' => 'TEXT',
            ],
            'is_approved' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'comment' => '1=approved, 0=pending moderation',
            ],
            'helpful_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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
        $this->forge->addKey('id', true);
        $this->forge->addKey(['resource_id', 'user_id']);
        $this->forge->addKey('parent_id');
        $this->forge->addKey('created_at');
        $this->forge->addForeignKey('parent_id', 'resource_comments', 'id', 'CASCADE', 'CASCADE', 'fk_resource_comments_parent');
        $this->forge->addForeignKey('resource_id', 'resources', 'id', 'CASCADE', 'CASCADE', 'fk_resource_comments_resource');
        $this->forge->createTable('resource_comments');

        // Resource contributors table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'resource_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'contributor_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'role' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['resource_id', 'contributor_id']);
        $this->forge->addForeignKey('resource_id', 'resources', 'id', 'CASCADE', 'CASCADE', 'resource_contributors_resource_id_foreign');
        $this->forge->createTable('resource_contributors');

        // Resource helpful votes table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'resource_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
                'comment' => 'If voting on a resource',
            ],
            'comment_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
                'comment' => 'If voting on a comment',
            ],
            'vote_type' => [
                'type' => 'ENUM',
                'constraint' => ['helpful', 'not_helpful'],
                'default' => 'helpful',
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
        $this->forge->addUniqueKey(['user_id', 'resource_id', 'comment_id']);
        $this->forge->addKey('resource_id');
        $this->forge->addKey('comment_id');
        $this->forge->addForeignKey('comment_id', 'resource_comments', 'id', 'CASCADE', 'CASCADE', 'fk_resource_helpful_votes_comment');
        $this->forge->addForeignKey('resource_id', 'resources', 'id', 'CASCADE', 'CASCADE', 'fk_resource_helpful_votes_resource');
        $this->forge->createTable('resource_helpful_votes');

        // User bookmarks table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'user_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'resource_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['user_id', 'resource_id']);
        $this->forge->addKey('resource_id');
        $this->forge->createTable('user_bookmarks');
    }

    public function down()
    {
        $this->forge->dropTable('user_bookmarks', true);
        $this->forge->dropTable('resource_helpful_votes', true);
        $this->forge->dropTable('resource_contributors', true);
        $this->forge->dropTable('resource_comments', true);
        $this->forge->dropTable('resources', true);
        $this->forge->dropTable('contributors', true);
        $this->forge->dropTable('resource_categories', true);
        $this->forge->dropTable('document_types', true);
    }
}
