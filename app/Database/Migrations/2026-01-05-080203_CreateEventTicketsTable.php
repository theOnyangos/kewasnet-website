<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEventTicketsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'booking_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'ticket_type_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'ticket_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'qr_code_data' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Unique data for QR code generation',
            ],
            'attendee_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'attendee_email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'used', 'cancelled'],
                'default' => 'active',
            ],
            'checked_in_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'checked_in_by' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
                'comment' => 'user_id of staff who checked in',
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
        $this->forge->addKey('booking_id');
        $this->forge->addKey('ticket_type_id');
        $this->forge->addKey('status');
        $this->forge->addUniqueKey('ticket_number');
        $this->forge->createTable('event_tickets');
    }

    public function down()
    {
        $this->forge->dropTable('event_tickets', true);
    }
}

