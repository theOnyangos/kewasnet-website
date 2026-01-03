<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateForumMembersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'VARCHAR',
                'constraint'     => 36,
                'null'           => false,
                'unique'         => true,
            ],
            'user_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'null'           => false,
            ],
            'forum_id' => [
                'type'           => 'VARCHAR',
                'constraint'     => 36,
                'null'           => false,
            ],
            'joined_at' => [
                'type'           => 'DATETIME',
                'null'           => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'forum_id']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('forum_id', 'forums', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('forum_members', true);
    }

    public function down()
    {
        $this->forge->dropTable('forum_members', true);
    }
}
