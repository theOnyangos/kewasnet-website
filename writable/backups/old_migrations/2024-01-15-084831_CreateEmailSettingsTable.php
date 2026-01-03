<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmailSettingsTable extends Migration
{
    public function up()
    {
        // Create email settings table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'host' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'encryption' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
            ],
            'port' => [
                'type' => 'INT',
                'constraint' => 5,
            ],
            'from_address' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'from_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('email_settings');
    }

    public function down()
    {
        // Delete email settings table
        $this->forge->dropTable('email_settings');
    }
}
