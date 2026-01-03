<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePillarLinksTable extends Migration
{
    public function up()
    {
        // Create pillars table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'pillar_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'links' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'description' => [
                'type' => 'VARCHAR',
                'constraint' =>255,
                'null' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ],
            'deleted_at' => [
                'type'   => 'DATETIME',
                'null'   => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('pillar_links');
    }

    public function down()
    {
        // Drop the table
        $this->forge->dropTable('pillar_links');
    }
}
