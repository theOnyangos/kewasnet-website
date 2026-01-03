<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePillarCategoriesTable extends Migration
{
    public function up()
    {
        // Create blog_categories table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
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
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('pillar_categories');
    }

    public function down()
    {
        // Drop table pillar_categories 
        $this->forge->dropTable('pillar_categories');
    }
}
