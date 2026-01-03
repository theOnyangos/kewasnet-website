<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBlogCommentRepliesTable extends Migration
{
    public function up()
    {
        // Create comment replies table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'comment_id' => [
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
            'reply' => [
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
        // $this->forge->addForeignKey('comment_id', 'blog_comments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('blog_comment_replies');
    }

    public function down()
    {
        // Drop the comment replies table
        $this->forge->dropTable('blog_comment_replies');
    }
}
