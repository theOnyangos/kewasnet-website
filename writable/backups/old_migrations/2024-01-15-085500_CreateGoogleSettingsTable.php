<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGoogleSettingsTable extends Migration
{
    public function up()
    {
        // Create google_settings table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'client_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'client_secret' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'redirect_uri' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'application_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('google_settings');
    }

    public function down()
    {
        // Drop google_settings table
        $this->forge->dropTable('google_settings');
    }
}
