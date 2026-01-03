<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBlogCommentsTable extends Migration
{
    public function up()
    {
        // Create the blog comments table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'blog_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'comment' => [
                'type' => 'TEXT'
            ],
            'is_published' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ],
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['anonymous', 'user'],
                'default' => 'anonymous'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addKey('id', true);
        // $this->forge->addForeignKey('blog_id', 'blogs', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('blog_comments');
    }

    public function down()
    {
        // Drop the blog comments table
        $this->forge->dropTable('blog_comments');
    }
}
