<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsBlockedToForumMembers extends Migration
{
    public function up()
    {
        // Add is_blocked column to forum_members table
        $fields = [
            'is_blocked' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
                'after' => 'joined_at'
            ],
        ];
        
        $this->forge->addColumn('forum_members', $fields);
    }

    public function down()
    {
        // Remove is_blocked column from forum_members table
        $this->forge->dropColumn('forum_members', 'is_blocked');
    }
}
