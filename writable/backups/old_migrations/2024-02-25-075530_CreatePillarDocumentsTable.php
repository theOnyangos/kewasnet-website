<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePillarDocumentsTable extends Migration
{
    public function up()
    {
        // Create pillar documents table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'pillar_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
            ],
            'file_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'file_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'file_type' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'file_size' => [
                'type' => 'VARCHAR',
                'constraint' => 255
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
                'type' => 'DATETIME',
                'null' => TRUE
            ],
        ]);

        $this->forge->addKey('id', TRUE);
        // $this->forge->addForeignKey('pillar_id', 'pillars', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pillar_documents');
    }

    public function down()
    {
        // Drop the pillar document table
        $this->forge->dropTable('pillar_documents');
    }
}
