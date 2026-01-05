<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEventTicketTypesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'event_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'Capacity for this ticket type, null means unlimited',
            ],
            'sales_start_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'sales_end_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default' => 'active',
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
        $this->forge->addKey('event_id');
        $this->forge->addKey('status');
        $this->forge->createTable('event_ticket_types');
    }

    public function down()
    {
        $this->forge->dropTable('event_ticket_types', true);
    }
}

