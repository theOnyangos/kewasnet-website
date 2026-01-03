<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSystemPillarsTable extends Migration
{
    public function up()
    {
        // Set default charset and collation for all tables
        $charset = 'utf8mb4';
        $collation = 'utf8mb4_unicode_ci';
        
        // Create pillars table
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
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'icon' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'content' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'image_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'button_text' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'Explore Resources',
            ],
            'button_link' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
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
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'is_private' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
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
        $this->forge->addKey('slug');
        $this->forge->addKey('is_active');
        $this->forge->createTable('pillars', true, ['ENGINE' => 'InnoDB', 'CHARSET' => $charset, 'COLLATE' => $collation]);

        // Resource Categories Table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'primary' => true,
                'default' => 'UUID()',
                'null' => false,
            ],
            'pillar_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'unsigned' => true,
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

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('pillar_id', 'pillars', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addKey(['pillar_id', 'slug']);
        $this->forge->createTable('resource_categories', true, ['ENGINE' => 'InnoDB', 'CHARSET' => $charset, 'COLLATE' => $collation]);

        // Document Types Table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'primary' => true,
                'default' => 'UUID()',
                'null' => false,
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

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('slug');
        $this->forge->createTable('document_types', true, ['ENGINE' => 'InnoDB', 'CHARSET' => $charset, 'COLLATE' => $collation]);

        // Resources Table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'primary' => true,
                'default' => 'UUID()',
                'null' => false,
            ],
            'pillar_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'unsigned' => true,
            ],
            'category_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'unsigned' => true,
                'null' => true,
            ],
            'document_type_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'unsigned' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'unique' => true,
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
                'constraint' => 11,
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

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('pillar_id', 'pillars', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('category_id', 'resource_categories', 'id', 'SET_NULL', 'SET_NULL');
        $this->forge->addForeignKey('document_type_id', 'document_types', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addKey('slug');
        $this->forge->addKey('is_featured');
        $this->forge->addKey('is_published');
        $this->forge->addKey('publication_year');
        $this->forge->createTable('resources', true, ['ENGINE' => 'InnoDB', 'CHARSET' => $charset, 'COLLATE' => $collation]);

        // User Bookmarks table
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
                'unsigned' => true,
            ],
            'resource_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'unsigned' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('resource_id', 'resources', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['user_id', 'resource_id']);
        $this->forge->createTable('user_bookmarks', true, ['ENGINE' => 'InnoDB', 'CHARSET' => $charset, 'COLLATE' => $collation]);

        // Contributors Table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'primary' => true,
                'default' => 'UUID()',
                'null' => false,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
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

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('contributors', true, ['ENGINE' => 'InnoDB', 'CHARSET' => $charset, 'COLLATE' => $collation]);

        // Resource Contributors Table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'primary' => true,
                'default' => 'UUID()',
                'null' => false,
            ],
            'resource_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'unsigned' => true,
            ],
            'contributor_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'unsigned' => true,
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

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('resource_id', 'resources', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('contributor_id', 'contributors', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addKey(['resource_id', 'contributor_id']);
        $this->forge->createTable('resource_contributors', true, ['ENGINE' => 'InnoDB', 'CHARSET' => $charset, 'COLLATE' => $collation]);
    }

    public function down()
    {
        $this->forge->dropTable('pillars');
        $this->forge->dropTable('resource_categories');
        $this->forge->dropTable('document_types');
        $this->forge->dropTable('resources');
        $this->forge->dropTable('user_bookmarks');
        $this->forge->dropTable('contributors');
        $this->forge->dropTable('resource_contributors');
    }
}
