<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFacebookSettingsTable extends Migration
{
    public function up()
    {
        // Create facebook_settings table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'page_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'page_access_token' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'app_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'app_secret' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'verification_token' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'webhook_url' => [
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
            ]
        ]);

        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('facebook_settings');
    }

    public function down()
    {
        // Delete facebook_settings table
        $this->forge->dropTable('facebook_settings');
    }
}
