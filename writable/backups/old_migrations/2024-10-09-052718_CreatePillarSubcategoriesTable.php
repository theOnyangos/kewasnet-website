<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePillarSubcategoriesTable extends Migration
{
    public function up()
    {
        // Drop table if it exists to avoid conflicts
        if ($this->db->tableExists('pillar_subcategories')) {
            $this->forge->dropTable('pillar_subcategories', true);
        }
        
        // Create pillar sub-categories table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'pillar_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'name' => [
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

        $this->forge->addKey('id', true);
        $this->forge->createTable('pillar_subcategories');
    }

    public function down()
    {
        // Dop pillar sub-categories table
        $this->forge->dropTable('pillar_subcategories');
    }
}
