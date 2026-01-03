<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEventTicketsTable extends Migration
{
    public function up()
    {
        // Create the event tickets table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'event_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'event_reg_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'ticket_code' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'ticket_type' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => 'standard',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('event_tickets');
    }

    public function down()
    {
        // Drop the event tickets table
        $this->forge->dropTable('event_tickets');
    }
}
