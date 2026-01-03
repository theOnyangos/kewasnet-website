<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMpesaSettingsTable extends Migration
{
    public function up()
    {
        // Create mpesa-settings table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'consumer_key' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE
            ],
            'consumer_secret' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE
            ],
            'consumer_endpoint' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE
            ],
            'business_short_code' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE
            ],
            'pass_key' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE
            ],
            'account_reference' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE
            ],
            'party_b' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE
            ],
            'callback_url' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE
            ],
            'callback_registered' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => TRUE
            ],
            'test_mode' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => TRUE
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
        $this->forge->createTable('mpesa_settings');
    }

    public function down()
    {
        // Delete mpesa-settings table
        $this->forge->dropTable('mpesa_settings');
    }
}
