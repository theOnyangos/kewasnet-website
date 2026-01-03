<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLectureLinksTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('lecture_links')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'lecture_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'link_title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'link_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
            ],
            'link_type' => [
                'type' => 'ENUM',
                'constraint' => ['resource', 'external', 'documentation'],
                'default' => 'resource',
            ],
            'order_index' => [
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

        $this->forge->addKey('id', true);
        $this->forge->addKey('lecture_id');
        $this->forge->createTable('lecture_links');
    }

    public function down()
    {
        $this->forge->dropTable('lecture_links');
    }
}
