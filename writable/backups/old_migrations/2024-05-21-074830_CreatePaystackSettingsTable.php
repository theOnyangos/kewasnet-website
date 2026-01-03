<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaystackSettingsTable extends Migration
{
    public function up()
    {
        // Create the paystack settings table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'public_key' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'secret_key' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'payment_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'status' => [
                'type' => 'INT',
                'constraint' => 1,
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
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('paystack_settings');
    }

    public function down()
    {
        // Drop the table
        $this->forge->dropTable('paystack_settings');
    }
}
