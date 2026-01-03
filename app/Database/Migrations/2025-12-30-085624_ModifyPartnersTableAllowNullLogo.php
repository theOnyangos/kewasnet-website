<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyPartnersTableAllowNullLogo extends Migration
{
    public function up()
    {
        // Modify partner_logo column to allow NULL
        $this->forge->modifyColumn('partners', [
            'partner_logo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        // Revert partner_logo column to NOT NULL
        $this->forge->modifyColumn('partners', [
            'partner_logo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
        ]);
    }
}
