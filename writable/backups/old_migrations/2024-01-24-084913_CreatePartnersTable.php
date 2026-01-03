<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePartnersTable extends Migration
{
    public function up()
    {
        // Create partners table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'partner_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'partner_logo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'partner_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
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
        $this->forge->createTable('partners');
    }

    public function down()
    {
        // Drop partners table
        $this->forge->dropTable('partners');
    }
}
