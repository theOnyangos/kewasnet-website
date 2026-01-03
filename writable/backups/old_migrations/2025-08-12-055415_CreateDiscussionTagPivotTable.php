<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

// Migration 5: Create Discussion Tag Pivot Table
class CreateDiscussionTagPivotTable extends Migration
{
    public function up()
    {
        // Drop table if it exists to avoid conflicts
        if ($this->db->tableExists('discussion_tag_pivot')) {
            $this->forge->dropTable('discussion_tag_pivot', true);
        }
        
        $this->forge->addField([
            'discussion_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'tag_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey(['discussion_id', 'tag_id'], true);
        $this->forge->addKey('tag_id');
        $this->forge->addForeignKey('discussion_id', 'discussions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tag_id', 'discussion_tags', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('discussion_tag_pivot');
    }

    public function down()
    {
        $this->forge->dropTable('discussion_tag_pivot');
    }
}
